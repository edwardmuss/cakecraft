<?php

/**
 * GIT DEPLOYMENT SCRIPT
 *
 * Used for automatically deploying websites via GitHub
 */

// Change to the directory of your git repository
chdir('/var/www/rebuetext'); // Set this to your Git repo path

// array of commands
$commands = array(
    'echo $PWD',
    'whoami',
    'git pull 2>&1', // Capture both standard output and errors
    'git status 2>&1',
    'git submodule sync 2>&1',
    'git submodule update 2>&1',
    'git submodule status 2>&1',
);

// exec commands
$output = '';
foreach ($commands as $command) {
    $tmp = shell_exec($command);

    // Display output
    $output .= "<span style=\"color: #6BE234;\">\$</span><span style=\"color: #729FCF;\">{$command}\n</span><br />";
    $output .= htmlentities(trim($tmp)) . "\n<br /><br />";
}
echo $output;
