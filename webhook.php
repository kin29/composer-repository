<?php

declare(strict_types=1);

$satisUrl = './satis.json';
$satisJson = json_decode(file_get_contents($satisUrl), true, 512, JSON_THROW_ON_ERROR);
$repositories = $satisJson['repositories'];

$payload = json_decode(file_get_contents("php://input"), true);

if ($payload['owner']['login'] !== 'kin29' || $payload['private']) {
    return;
}

$repositoryName = $payload['repository']['full_name']; // kin29/xxxx
$inputGitHubUrl = 'https://github.com/' . $repositoryName;

$hasRepository = false;
foreach ($satisJson['repositories'] as $repository) {
    if ($repository['url'] === $inputGitHubUrl) {
        $hasRepository = true;
        break;
    }
}

if (!$hasRepository) {
    $satisJson['repositories'][] = [
        'type' => 'vcs',
        'url' => $inputGitHubUrl,
    ];
    file_put_contents($satisUrl, json_encode($satisJson, JSON_THROW_ON_ERROR));
}

//satis build実行
exec('./vendor/bin/satis build satis.json web');
