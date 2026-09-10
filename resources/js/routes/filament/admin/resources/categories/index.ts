import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
export const index = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
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
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
index.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
index.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
const indexForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
indexForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\ListCategories::__invoke
* @see app/Admin/Resources/Categories/Pages/ListCategories.php:7
* @route '/admin/{tenant}/categories'
*/
indexForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
export const create = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
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
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
create.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
create.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
const createForm = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
createForm.get = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\CreateCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/CreateCategory.php:7
* @route '/admin/{tenant}/categories/create'
*/
createForm.head = (args: { tenant: string | number | { uuid: string | number } } | [tenant: string | number | { uuid: string | number } ] | string | number | { uuid: string | number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
export const edit = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
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
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
edit.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
edit.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
const editForm = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
editForm.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
editForm.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

const categories = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit),
}

export default categories