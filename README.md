[![Build Status](https://travis-ci.com/gromo/dkron-php-adapter.svg?branch=master)](https://travis-ci.com/gromo/dkron-php-adapter)
[![PHP](https://img.shields.io/badge/PHP-%5E7.0-blue.svg)](https://packagist.org/packages/gromo/dkron-php-adapter)
[![Dkron version](https://img.shields.io/badge/Dkron-v0.10.0-green.svg)](https://github.com/victorcoder/dkron/releases/tag/v0.10.0)



# Dkron PHP Adapter

Adapter to communicate with [Dkron](https://dkron.io).

Please read [API](https://dkron.io/usage/api/) for usage details

### Install:
- add `"gromo/dkron-php-adapter": "dev-master"` to your project `composer.json`
- run `composer install`

### Use:
```php
$httpClient = new \GuzzleHttp\Client([
    'base_uri' => 'http://localhost:8080/',
]);

$api = new \Dkron\Api($httpClient);

// get status
$status = $api->getStatus();

// get all jobs
$jobs = $api->getJobs();

// create & save job
$newJob = new \Dkron\Models\Job('my-job', '@every 5m');
$newJob->setExecutor('shell');
$newJob->setExecutorConfig([
    'command' => 'ls -la /'
]);
$api->saveJob($newJob);

// create job from parsed json
$newJobFromArray = \Dkron\Models\Job::createFromArray([
    'name' => 'job name',
    'schedule' => 'job schedule',
    'executor' => 'shell',
    'executor_config' => [
        'command' => 'ls -la /tmp',
    ],
    // other parameters
]);

// get job data as json string
$json = json_encode($newJobFromArray);

// get job by name
$existingJob = $api->getJob('my-job');

// run job by name
$api->runJob($existingJob->getName());

// get job executions
$executions = $api->getJobExecutions($existingJob->getName());

// delete job by name
$api->deleteJob($existingJob->getName());

// get current leader node
$leader = $api->getLeader();

// get all nodes
$members = $api->getMembers();


```

### API methods

All URIs are relative to *http://localhost:8080/v1*

Method | Description | HTTP request
------------ | ------------- | ------------- 
*getStatus* | Get status | [**GET** /](https://dkron.io/usage/api/#get)
*getJobs* | Get all jobs | [**GET** /jobs](https://dkron.io/usage/api/#get-jobs)
*saveJob* | Save job | [**POST** /jobs](https://dkron.io/usage/api/#post-jobs)
*getJob* | Get job info by name | [**GET** /jobs/{job_name}](https://dkron.io/usage/api/#get-jobs-job-name)
*runJob* | Run job by name | [**POST** /jobs/{job_name}](https://dkron.io/usage/api/#post-jobs-job-name)
*deleteJob* | Delete job by name | [**DELETE** /jobs/{job_name}](https://dkron.io/usage/api/#delete-jobs-job-name)
*getJobExecutions* | Get job executions by job name | [**GET** /jobs/{job_name}/executions](https://dkron.io/usage/api/#get-jobs-job-name-executions)
*getLeader* | Get leader | [**GET** /leader](https://dkron.io/usage/api/#get-leader)
*leave* | Force the node to leave the cluster | [**GET** /leave](https://dkron.io/usage/api/#get-leave)
*getMembers* | Get members | [**GET** /members](https://dkron.io/usage/api/#get-members)

