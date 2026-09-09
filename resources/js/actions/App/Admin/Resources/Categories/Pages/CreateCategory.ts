import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
const CreateCategory = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCategory.url(args, options),
    method: 'get',
})

CreateCategory.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
CreateCategory.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return CreateCategory.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
CreateCategory.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCategory.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
CreateCategory.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateCategory.url(args, options),
    method: 'head',
})

export default CreateCategory