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
#my $expressionfile="expression.file";
my $expressionfile="";
#my $expressiontype="illumina"; 
my $expressiontype="";
#my $predictortype="general";
my $predictortype="general";
#my $agefile="age.file";
my $agefile="";
my $status="";

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

$dbh->disconnect();


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