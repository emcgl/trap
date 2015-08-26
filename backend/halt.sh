#!/bin/bash

export SGE_ROOT=/opt/sge

#Config
backend="/home/marijn/workspace/tragca/backend";
userdata="/home/marijn/workspace/tragca/data/users";
qdel="${SGE_ROOT}/bin/lx-amd64/qdel";


#Parameters
sgeid=$1;					# Job Id

${qdel} ${sgeid}