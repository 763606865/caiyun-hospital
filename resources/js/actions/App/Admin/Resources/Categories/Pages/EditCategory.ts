import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
const EditCategory = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCategory.url(args, options),
    method: 'get',
})

EditCategory.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/categories/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
EditCategory.url = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions) => {
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

    return EditCategory.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
EditCategory.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCategory.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Categories\Pages\EditCategory::__invoke
* @see app/Admin/Resources/Categories/Pages/EditCategory.php:7
* @route '/admin/{tenant}/categories/{record}/edit'
*/
EditCategory.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditCategory.url(args, options),
    method: 'head',
})

export default EditCategory