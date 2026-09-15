<?php

namespace Ugarit\Octane\Concerns;

trait ProvidesDefaultConfigurationOptions
{
    /**
     * Get the listeners that will prepare the Ugarit application for a new request.
     */
    public static function prepareApplicationForNextRequest(): array
    {
        return [
            \Ugarit\Octane\Listeners\FlushLocaleState::class,
            \Ugarit\Octane\Listeners\FlushQueuedCookies::class,
            \Ugarit\Octane\Listeners\FlushSessionState::class,
            \Ugarit\Octane\Listeners\FlushAuthenticationState::class,
            \Ugarit\Octane\Listeners\EnforceRequestScheme::class,
            \Ugarit\Octane\Listeners\EnsureRequestServerPortMatchesScheme::class,
            \Ugarit\Octane\Listeners\GiveNewRequestInstanceToApplication::class,
            \Ugarit\Octane\Listeners\GiveNewRequestInstanceToPaginator::class,
        ];
    }

    /**
     * Get the listeners that will prepare the Ugarit application for a new operation.
     */
    public static function prepareApplicationForNextOperation(): array
    {
        return [
            \Ugarit\Octane\Listeners\CreateConfigurationSandbox::class,
            \Ugarit\Octane\Listeners\CreateUrlGeneratorSandbox::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToAuthorizationGate::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToBroadcastManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToDatabaseManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToDatabaseSessionHandler::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToFilesystemManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToHttpKernel::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToLogManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToMailManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToNotificationChannelManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToPipelineHub::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToCacheManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToSessionManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToQueueManager::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToRouter::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToValidationFactory::class,
            \Ugarit\Octane\Listeners\GiveNewApplicationInstanceToViewFactory::class,
            \Ugarit\Octane\Listeners\FlushDatabaseRecordModificationState::class,
            \Ugarit\Octane\Listeners\FlushDatabaseQueryLog::class,
            \Ugarit\Octane\Listeners\RefreshQueryDurationHandling::class,
            \Ugarit\Octane\Listeners\FlushArrayCache::class,
            \Ugarit\Octane\Listeners\FlushLogContext::class,
            \Ugarit\Octane\Listeners\FlushMonologState::class,
            \Ugarit\Octane\Listeners\FlushStrCache::class,
            \Ugarit\Octane\Listeners\FlushTranslatorCache::class,
            \Ugarit\Octane\Listeners\FlushVite::class,

            // First-Party Packages...
            \Ugarit\Octane\Listeners\PrepareInertiaForNextOperation::class,
            \Ugarit\Octane\Listeners\PrepareLivewireForNextOperation::class,
            \Ugarit\Octane\Listeners\PrepareScoutForNextOperation::class,
            \Ugarit\Octane\Listeners\PrepareSocialiteForNextOperation::class,
        ];
    }

    /**
     * Get the container bindings / services that should be pre-resolved by default.
     */
    public static function defaultServicesToWarm(): array
    {
        return [
            'auth',
            'cache',
            'cache.store',
            'config',
            'cookie',
            'db',
            'db.factory',
            'db.transactions',
            'encrypter',
            'files',
            'hash',
            'log',
            'router',
            'routes',
            'session',
            'session.store',
            'translator',
            'url',
            'view',
        ];
    }
}
