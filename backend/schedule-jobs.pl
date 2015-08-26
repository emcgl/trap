#!/usr/bin/perl -w

use strict;
use DBI;

#Config Variables
my $userdata="/home/marijn/workspace/tragca/data/users";

#DB Variables
my $DBName                  = "tragca";
my $DBUserName              = "tragca";
my $DBPassword              = "tr12321ca";

#From database 'jobs' table
my $id="";
my $uid="";
my $name="";
my $expressionfile="";
my $expressiontype="";
my $predictortype="";
my $agefile="";
my $status="";

#SGEInfo Variables
my $sgeid;
my $errornr;
my $outputnr;


my $dbh = DBI->connect("DBI:mysql:$DBName;localhost;port=3306","$DBUserName","$DBPassword", {'RaiseError' => 0}) or die "Cannot connect to database!\n";

#
# Iterate all "defined" jobs, make them "running" and schedule them!
#

print "Schedule 'defined' jobs!\n";

my $sql = "SELECT id, uid, name, expressionfile, expressiontype, predictortype, agefile, status FROM jobs WHERE status='defined'";
my $sth = $dbh->prepare( $sql ) or die "Can't prepare db statement!";
my $rc = $sth->execute or die "Can't execute statement!";

while(($id, $uid, $name, $expressionfile, $expressiontype, $predictortype, $agefile, $status) = $sth->fetchrow_array) {
			
	print "Processing job: ",$id,", named: ", $name," ..";
	
	#Fixed!
	$expressionfile="expression.file";
	$agefile="age.file";
	
	#Update Database and run!
	my $sql2 = "UPDATE jobs SET status = 'scheduled' WHERE id=".$dbh->quote ($id);		
	my $rows_affected = $dbh->do($sql2);
	$rows_affected == 1 or die "db entry not found!",$id; 
	
	print "scheduled!\n";	
	scheduleJob();	
}

#
# Iterate all scheduled jobs, see if they are running!
#

print "Check 'scheduled' jobs jobs!\n";

$sql = "SELECT id, uid, name, expressionfile, expressiontype, predictortype, agefile, status FROM jobs WHERE status='scheduled'";
$sth = $dbh->prepare( $sql ) or die "Can't prepare db statement!";
$rc = $sth->execute or die "Can't execute statement!";

while(($id, $uid, $name, $expressionfile, $expressiontype, $predictortype, $agefile, $status) = $sth->fetchrow_array) {

	print "Analysing scheduled job ".$id."..";
	
	my $errornr=0;
	my $outputnr=0;
	
	$sgeid = recoverSGEID();
	
	if($sgeid gt 0) {
		
		print "has gridengine job nr: ".$sgeid."..";
		
		#Update Database entry to running!
		my $sql2 = "UPDATE jobs SET status = 'running' WHERE id=".$dbh->quote($id);		
		my $rows_affected = $dbh->do($sql2);
		$rows_affected == 1 or die "db entry not found, not able to update status!",$id;
	} else { 
		print "no output (yet!)..\n";
	}
}

#
# Iterate all running jobs, see if they are ready!
#

print "Check 'running' jobs!\n";

$sql = "SELECT id, uid, name, expressionfile, expressiontype, predictortype, agefile, status FROM jobs WHERE status='running'";
$sth = $dbh->prepare( $sql ) or die "Can't prepare db statement!";
$rc = $sth->execute or die "Can't execute statement!";

while(($id, $uid, $name, $expressionfile, $expressiontype, $predictortype, $agefile, $status) = $sth->fetchrow_array) {

	print "Analysing running job ".$id."..";

	#Is there a result file?
	my $outputtxt="";
	if($predictortype eq "scaled"){ $outputtxt = "output.scaled.".$id.".txt";}	
	elsif($predictortype eq "general") { $outputtxt = "output.general.".$id.".txt";}
	else { die "Unknown predictortype:".$predictortype."\n";}
		
	if( -e $userdata."/".$uid."/".$id."/".$outputtxt ) {
		
		#Update Database entry to finished!
		my $sql2 = "UPDATE jobs SET status = 'finished' WHERE id=".$dbh->quote($id);		
		my $rows_affected = $dbh->do($sql2);
		$rows_affected == 1 or die "db entry not found, not able to update status!",$id; 
		
			print "finished!\n";
		
	} else {
		print "not finished!\n";
	}
}


#
# Iterate all to-halt jobs!
#

print "Check 'halt' jobs!\n";

$sql = "SELECT id, uid, name, expressionfile, expressiontype, predictortype, agefile, status FROM jobs WHERE status='halt'";
$sth = $dbh->prepare( $sql ) or die "Can't prepare db statement!";
$rc = $sth->execute or die "Can't execute statement!";

while(($id, $uid, $name, $expressionfile, $expressiontype, $predictortype, $agefile, $status) = $sth->fetchrow_array) {

	print "Halting job ".$id."..";

	$sgeid = recoverSGEID();
	
	if($sgeid == 0) {
		die("Can't find SGE id for 'halt' job ".$id."..should have output and error file\n");
	}

	#Update Database entry to finished!
	my $sql2 = "UPDATE jobs SET status = 'halted' WHERE id=".$dbh->quote($id);		
	my $rows_affected = $dbh->do($sql2);
	$rows_affected == 1 or die "db entry not found, not able to update status!",$id; 
	
	cancelJob();
	
}	


$dbh->disconnect();

print "Done!\n";

exit(1);

sub scheduleJob { 

	my $formulafile="";
	my $rscript="";

	if($expressiontype eq "illumina") {
		if($predictortype eq "general") {
			$formulafile="FORMULA-GENERAL-PREDICTOR-ILMN_ID.txt";
			$rscript="GENERAL-PREDICTOR-ILMN.R";
		} elsif ($predictortype eq "scaled") {
			$formulafile="FORMULA-SCALED-GENERAL-PREDICTOR-ILMN_ID.txt";
			$rscript="SCALED-PREDICTOR-ILMN.R";
		} else {
			die("Unknown predictor file type!");
		}
	} elsif ($expressiontype eq "gene") {
		if($predictortype eq "general") {
			$formulafile="FORMULA-GENERAL-PREDICTOR-GENE_ID.txt";
			$rscript="GENERAL-PREDICTOR-GENE.R";
		} elsif ($predictortype eq "scaled") {
			$formulafile="FORMULA-SCALED-GENERAL-PREDICTOR-GENE_ID.txt";
			$rscript="SCALED-PREDICTOR-GENE.R";
		} else {
			die("Unknown predictor file type!");
		}
	} else {
		die("Unknown expression file type!");
	}

	my @args="";

	if($predictortype eq "general") {
	  
	  	print "Scheduled job ".$name." (".$id.")\n";
	  
	  	my $outputtxt = "output.general.".$id.".txt";
	  
	  	@args = ( "./schedule-general.sh",
	  			  $id,
	  			  $uid,
	  			  $rscript,
	  			  $expressionfile,
	  			  $agefile,
	  			  $formulafile,
	  			  $outputtxt
	  			);
	
	} elsif($predictortype eq "scaled") {
		
	  	print "Scheduled job ".$name." (".$id.")\n";
	  
	  	my $outputtxt = "output.scaled.".$id.".txt";
	  
	  	@args = ( "./schedule-scaled.sh",
	  			  $id,
	  			  $uid,
	  			  $rscript,
	  			  $expressionfile,
	  			  $formulafile,
	  			  $outputtxt
	  			);
		
	}		
	 
system(@args) == 0 or die "system @args failed: $?";

}

sub recoverSGEID {

	$errornr=0;
	$outputnr=0;

	opendir(DIR, $userdata."/".$uid."/".$id );
	my @files = readdir DIR;
	foreach my $file (@files)
	{
		#Skip . .. and search barcode dirs
		if($file eq "."){next};
		if($file eq ".."){next};
		if($file !~ "Rscript"){next};
		
		$file =~ /Rscript\.([e|o])(\d*)/;

		if($1 eq 'e') {
			$errornr=$2;
		} elsif($1 eq 'o'){
			$outputnr=$2;
		} else {
			die("Found a strange grid engine Rscript output file:".$file."\n")
		}
	}
		if($errornr!=$outputnr) { die "Found different grid engine jobs outputs: ".$errornr.", and ".$outputnr.".\n"};
	
	return $errornr;
}

sub cancelJob {
		my @args="";

	  	@args = ( "./halt.sh",
	  			  $sgeid);

		system(@args) == 0 or die "system @args failed: $?";
}	