""" Tools for working with subprocesses """

import subprocess, shlex

def run(cmd):
    "Run shell command with arguments and return stdout text"
    res = ""
    for subcommand in cmd.split("&&"):
        res+=_execute(subcommand)
        
    return res

class ExecError(Exception):
    def __init__(self, returncode, cmd, stderr):
        self.returncode = returncode
        self.cmd = cmd
        self.stderr = stderr
    def __str__(self):
        return "Command '%s' returned non-zero exit status %d stderr: '%s'" % (self.cmd, self.returncode, self.stderr)
        
def _execute(cmd):
    pipes = cmd.split('|')
    p = subprocess.Popen(shlex.split(pipes[0]), stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    i = 1
    while i<len(pipes):
        p = subprocess.Popen(shlex.split(pipes[i]), stdin=p.stdout, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        i += 1
    
    stdout, stderr = p.communicate()
    
    if p.returncode != 0:
        raise ExecError(p.returncode, cmd, stderr)
    
    return stdout