#!/bin/bash

#Config
backend="/trap/backend";
userdata="/trap/data/users";
qsub="/usr/bin/qsub -V -cwd -b y";
Rscript="/usr/bin/Rscript";

#Parameters
id=$1;				# Job Id
uid=$2;				# User Id
rscript=$3;			# R script file
expressionfile=$4;		# Expression file
formulafile=$5;			# Formula File
outputtxt=$6;			# Output

cd $userdata/$uid/$id;	  	  	
	  	
${qsub} ${Rscript} ${backend}/${rscript} expressionfile=${expressionfile} \
										 formula=${backend}/${formulafile} \
										 outputtxt=${outputtxt};

		
