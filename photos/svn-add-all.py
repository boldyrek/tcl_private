#!/usr/bin/env python
import tt_os

def main():
    #tt_os.run('svn up')
    status = tt_os.run('svn status').split('\n')
    if not status: return
    for line in status:
        line = line.strip()
        if not line: continue
        if line[0] == '?':
            path = line[1:].strip()
            tt_os.run('svn add "'+path+'"')
    tt_os.run('svn commit -m "svn-add-all added files"')   
    #tt_os.run('svn up')


if __name__=='__main__':
    main()

