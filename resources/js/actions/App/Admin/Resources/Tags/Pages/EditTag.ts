import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Tags\Pages\EditTag::__invoke
* @see app/Admin/Resources/Tags/Pages/EditTag.php:7
* @route '/admin/{tenant}/tags/{record}/edit'
*/
const EditTag = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditTag.url(args, options),
    method: 'get',
})

EditTag.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/tags/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Tags\Pages\EditTag::__invoke
* @see app/Admin/Resources/Tags/Pages/EditTag.php:7
* @route '/admin/{tenant}/tags/{record}/edit'
*/
EditTag.url = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions) => {
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

    return EditTag.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Tags\Pages\EditTag::__invoke
* @see app/Admin/Resources/Tags/Pages/EditTag.php:7
* @route '/admin/{tenant}/tags/{record}/edit'
*/
EditTag.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditTag.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Tags\Pages\EditTag::__invoke
* @see app/Admin/Resources/Tags/Pages/EditTag.php:7
* @route '/admin/{tenant}/tags/{record}/edit'
*/
EditTag.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditTag.url(args, options),
    method: 'head',
})

export default EditTag