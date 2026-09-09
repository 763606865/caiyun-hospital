import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
const EditContent = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditContent.url(args, options),
    method: 'get',
})

EditContent.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/contents/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
EditContent.url = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions) => {
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

    return EditContent.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
EditContent.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditContent.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Contents\Pages\EditContent::__invoke
* @see app/Admin/Resources/Contents/Pages/EditContent.php:7
* @route '/admin/{tenant}/contents/{record}/edit'
*/
EditContent.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditContent.url(args, options),
    method: 'head',
})

export default EditContent