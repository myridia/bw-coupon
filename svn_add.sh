svn st | grep '^\?' | awk '{$1="";print substr($0,2)}' | xargs svn add
