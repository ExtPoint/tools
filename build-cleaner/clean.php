<?php

$config = array(
    'projectDir' => __DIR__,
    'trashDir' => __DIR__ . '/tmp/trash',
    'lastCount' => 2,
    'execute' => isset($argv[1]) && $argv[1] == 'm',
);

$versions = array(
    'current' => null,
    'all' => array(),
    'last' => array(),
    'old' => array(),
);

// Store current and all
foreach (scandir($config['projectDir']) as $dirName) {
    if ($dirName[0] === '.') {
        continue;
    }

    $dirPath = $config['projectDir'] . '/' . $dirName;
    if ($dirName === 'current') {
        $versions['current'] = pathinfo(realpath($dirPath), PATHINFO_BASENAME);
        continue;
    }

    if (!is_link($dirPath) && is_dir($dirPath) && preg_match('/-([0-9]+)$/', $dirName, $match)) {
        $versions['all'][$match[1]] = $dirName;
    }
}
krsort($versions['all']);
$versions['all'] = array_values($versions['all']);

// Store last and old (before current build)
$i = -1;
foreach ($versions['all'] as $dirName) {
    if ($dirName === $versions['current']) {
        $i = 0;
        continue;
    }

    if ($i !== -1) {
        if ($i < $config['lastCount']) {
            $versions['last'][] = $dirName;
        } else {
            $versions['old'][] = $dirName;
        }
        $i++;
    }
}

// Show help info
if (!$config['execute']) {
    echo "\nNote: Append 'm' argument for real move dirs to trash.\n\n";

    echo "Current version:\n";
    echo "  " . $config['projectDir'] . '/' . $versions['current'] . "\n";

    echo "Last versions:\n";
    if (count($versions['last']) > 0) {
        foreach ($versions['last'] as $dirName) {
            echo "  " . $config['projectDir'] . '/' . $dirName . "\n";
        }
    } else {
        echo "  -- none --\n";
    }

    echo "Old versions (will be move):\n";
    if (count($versions['old']) > 0) {
        foreach ($versions['old'] as $dirName) {
            echo "  " . $config['projectDir'] . '/' . $dirName . "\n";
        }
    } else {
        echo "  -- none --\n";
    }
} else {
    // Do move
    foreach ($versions['old'] as $dirName) {
        $dirPath = $config['projectDir'] . '/' . $dirName;
            $config['trashDir'] = rtrim($config['trashDir'], '/');
            if (!is_dir($config['trashDir']) && !@mkdir($config['trashDir'], 777, true)) {
                echo "Can not create trash dir in path: " . $config['trashDir'] . "\n";
                exit();
            }

            $newDirPath = $config['trashDir'] . '/' . $dirName;

            echo "Moved: '{$dirPath}' to '{$newDirPath}'\n";
            rename($dirPath, $newDirPath);
    }
}