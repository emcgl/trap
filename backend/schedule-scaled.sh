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
formulafile=$5;			# Formula File
outputtxt=$6;			# Output

cd $userdata/$uid/$id;	  	  	
	  	
${qsub} ${Rscript} ${backend}/${rscript} expressionfile=${expressionfile} \
										 formula=${backend}/${formulafile} \
										 outputtxt=${outputtxt};

		