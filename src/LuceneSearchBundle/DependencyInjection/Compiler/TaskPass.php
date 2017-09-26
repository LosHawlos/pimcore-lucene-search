<?php

namespace LuceneSearchBundle\DependencyInjection\Compiler;

use LuceneSearchBundle\Task\TaskManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class TaskPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(TaskManager::class)) {
            return;
        }

        $definition = $container->findDefinition(TaskManager::class);
        $tasks = $this->findAndSortTaggedServices('lucene_search.task', $container);

        if (empty($tasks)) {
            throw new RuntimeException('You must tag at least one tak as "lucene_search.task".');
        }

        foreach ($tasks as $id => $task) {
            $definition->addMethodCall('addTask', [$task, (string)$task]);
        }
    }
}