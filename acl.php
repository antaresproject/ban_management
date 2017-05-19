<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actions = [
    new Action('ban_management.rules.index', 'List Rules'),
    new Action('ban_management.rules.create', 'Add Rule'),
    new Action('ban_management.rules.store', 'Add Rule'),
    new Action('ban_management.rules.edit', 'Update Rule'),
    new Action('ban_management.rules.update', 'Update Rule'),
    new Action('ban_management.rules.destroy', 'Delete Rule'),
    new Action('ban_management.bannedemails.index', 'List Banned Emails'),
    new Action('ban_management.bannedemails.create', 'Add Banned Email'),
    new Action('ban_management.bannedemails.store', 'Add Banned Email'),
    new Action('ban_management.bannedemails.edit', 'Update Banned Email'),
    new Action('ban_management.bannedemails.update', 'Update Banned Email'),
    new Action('ban_management.bannedemails.destroy', 'Delete Banned Email'),
];

$adminActions = array_merge($actions, [
    new Action('ban_management.configuration', 'Configuration')]);

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $adminActions);
$permissions->add(Role::member()->name, $actions);

return $permissions;
