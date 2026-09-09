import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\OperationLogs\Pages\ListOperationLogs::__invoke
* @see app/Admin/Resources/OperationLogs/Pages/ListOperationLogs.php:7
* @route '/admin/{tenant}/operation-logs'
*/
const ListOperationLogs = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListOperationLogs.url(args, options),
    method: 'get',
})

ListOperationLogs.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/operation-logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\OperationLogs\Pages\ListOperationLogs::__invoke
* @see app/Admin/Resources/OperationLogs/Pages/ListOperationLogs.php:7
* @route '/admin/{tenant}/operation-logs'
*/
ListOperationLogs.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tenant: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { tenant: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            tenant: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenant: typeof args.tenant === 'object'
        ? args.tenant.uuid
        : args.tenant,
    }

    return ListOperationLogs.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\OperationLogs\Pages\ListOperationLogs::__invoke
* @see app/Admin/Resources/OperationLogs/Pages/ListOperationLogs.php:7
* @route '/admin/{tenant}/operation-logs'
*/
ListOperationLogs.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListOperationLogs.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\OperationLogs\Pages\ListOperationLogs::__invoke
* @see app/Admin/Resources/OperationLogs/Pages/ListOperationLogs.php:7
* @route '/admin/{tenant}/operation-logs'
*/
ListOperationLogs.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListOperationLogs.url(args, options),
    method: 'head',
})

export default ListOperationLogs