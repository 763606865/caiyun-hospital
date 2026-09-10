import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
const CreateTag = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTag.url(args, options),
    method: 'get',
})

CreateTag.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/tags/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
CreateTag.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return CreateTag.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
CreateTag.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateTag.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
CreateTag.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateTag.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
const CreateTagForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTag.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
CreateTagForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTag.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Tags\Pages\CreateTag::__invoke
* @see app/Admin/Resources/Tags/Pages/CreateTag.php:7
* @route '/admin/{tenant}/tags/create'
*/
CreateTagForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateTag.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateTag.form = CreateTagForm

export default CreateTag