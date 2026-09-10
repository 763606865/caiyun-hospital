import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
const CreateContent = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateContent.url(args, options),
    method: 'get',
})

CreateContent.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
CreateContent.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return CreateContent.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
CreateContent.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateContent.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
CreateContent.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateContent.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
const CreateContentForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateContent.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
CreateContentForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateContent.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
CreateContentForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: CreateContent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

CreateContent.form = CreateContentForm

export default CreateContent