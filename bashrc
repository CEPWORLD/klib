# .bashrc
#$:0.0.1
PATH=${HOME}/bin:${PATH}
export PATH

#SVN
export SVN_EDITOR=/usr/bin/vim

# alias
alias ls='ls -F'

log () {
   cmd=( $* )
   datestr=$(date +%Y%m%d-%H%M%S)
   echo "cmd : $*" > ~/script.log/${cmd[0]}-${datestr}.log
   echo "pwd : $pwd" >> ~/script.log/${cmd[0]}-${datestr}.log
   $@ | tee -a ~/script.log/${cmd[0]}-${datestr}.log
}

svn() {
   cmd=( $* )
   datestr=$(date +%Y%m%d-%H%M%S)
   echo "pwd: $(pwd)" > ~/script.log/svn-${cmd[0]}-${datestr}.log
   /usr/bin/svn $* | tee -a ~/script.log/svn-${cmd[0]}-${datestr}.log
}

rm() {
   cmd=( $* )
   datestr=$(date +%Y%m%d-%H%M%S)
   cmd_str=$(basename ${cmd[$((${#cmd[*]}-1))]})
   echo "pwd: $(pwd)" > ~/script.log/rm-$cmd_str-${datestr}.log
   echo "cmd: $0 $*" >> ~/script.log/rm-$cmd_str-${datestr}.log
   /bin/rm $* | tee -a ~/script.log/rm-$cmd_str-${datestr}.log
}

cp() {
   cmd=( $@ )
   datestr=$(date +%Y%m%d-%H%M%S)
   cmd_str=$(basename ${cmd[$((${#cmd[*]}-1))]})
   echo "pwd: $(pwd)" > ~/script.log/cp-$cmd_str-${datestr}.log
   echo "cmd: $0 $@" >> ~/script.log/cp-$cmd_str-${datestr}.log
   /bin/cp $@ | tee -a ~/script.log/cp-$cmd_str-${datestr}.log
}

mv() {
   cmd=( $@ )
   datestr=$(date +%Y%m%d-%H%M%S)
   cmd_str=$(basename ${cmd[$((${#cmd[*]}-1))]})
   echo "pwd: $(pwd)" > ~/script.log/mv-$cmd_str-${datestr}.log
   echo "cmd: $0 $@" >> ~/script.log/mv-$cmd_str-${datestr}.log
   /bin/mv $@ | tee -a ~/script.log/mv-$cmd_str-${datestr}.log
}


clr() {
   list="$(find ~/script.log/ -ctime +90 -type f )"
   /bin/rm $list
}
