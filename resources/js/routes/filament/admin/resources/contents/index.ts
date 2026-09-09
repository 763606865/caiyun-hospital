import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
export const index = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
index.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
index.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\ListContents::__invoke
* @see app/Admin/Resources/Contents/Pages/ListContents.php:7
* @route '/admin/{tenant}/contents'
*/
index.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
export const create = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
create.url = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions) => {
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

    return create.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
create.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\CreateContent::__invoke
* @see app/Admin/Resources/Contents/Pages/CreateContent.php:7
* @route '/admin/{tenant}/contents/create'
*/
create.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
export const edit = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
edit.url = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            tenant: args[0],
            record: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenant: typeof args.tenant === 'object'
        ? args.tenant.uuid
        : args.tenant,
        record: args.record,
    }

    return edit.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
edit.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
edit.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

const contents = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit),
}

export default contents