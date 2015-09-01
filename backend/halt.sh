#!/bin/bash

#Config
backend="/trap/backend";
userdata="/trap/data/users";
qdel="/usr/bin/qdel";

#Parameters
sgeid=$1;					# Job Id

${qdel} ${sgeid}
