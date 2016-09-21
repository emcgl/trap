#!/bin/bash

#Config
. trap-backend.config

#Parameters
id=$1;				# Job Id
uid=$2;				# User Id
rscript=$3;			# R script file
expressionfile=$4;	# Expression file
formulafile=$5;		# Formula File
outputtxt=$6;		# Output

cd $userdata/$uid/$id;	  	  	
	  	
${qsub} ${Rscript} ${backend}/${rscript} expressionfile=${expressionfile} \
										 formula=${backend}/${formulafile} \
										 outputtxt=${outputtxt} \
										 backend=${backend};

		
