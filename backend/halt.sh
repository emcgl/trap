#!/bin/bash


#Config
. trap-backend.config

#Parameters
sgeid=$1;					# Job Id

${qdel} ${sgeid}
