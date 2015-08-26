#!/bin/bash

export SGE_ROOT=/opt/sge

#Config
backend="/home/marijn/workspace/tragca/backend";
userdata="/home/marijn/workspace/tragca/data/users";
qsub="${SGE_ROOT}/bin/lx-amd64/qsub -V -cwd -b y";
Rscript="/opt/software/R/bin/Rscript";

#Parameters
id=$1;					# Job Id
uid=$2;					# User Id
rscript=$3;				# R script file
expressionfile=$4;		# Expression file
agefile=$5;				# Age File
formulafile=$6;			# Formula File
outputtxt=$7;			# Output

cd $userdata/$uid/$id;	  	  	
	  	
${qsub} ${Rscript} ${backend}/${rscript} expressionfile=${expressionfile} \
										 agefile=${agefile} \
										 formula=${backend}/${formulafile} \
										 outputtxt=$outputtxt;
		
