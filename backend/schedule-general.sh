#!/bin/bash

#Config
. trap-backend.config

#Parameters
id=$1;				# Job Id
uid=$2;				# User Id
rscript=$3;			# R script file
expressionfile=$4;	# Expression file
agefile=$5;			# Age File
formulafile=$6;		# Formula File
outputtxt=$7;		# Output

cd $userdata/$uid/$id;	  	  	
	  	
${qsub} ${Rscript} ${backend}/${rscript} expressionfile=${expressionfile} \
										 agefile=${agefile} \
										 formula=${backend}/${formulafile} \
										 outputtxt=${outputtxt} \
										 backend=${backend};
		
