import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Admin\Resources\Media\Pages\EditMedia::__invoke
* @see app/Admin/Resources/Media/Pages/EditMedia.php:7
* @route '/admin/{tenant}/media/{record}/edit'
*/
const EditMedia = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditMedia.url(args, options),
    method: 'get',
})

EditMedia.definition = {
    methods: ["get","head"],
    url: '/admin/{tenant}/media/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Admin\Resources\Media\Pages\EditMedia::__invoke
* @see app/Admin/Resources/Media/Pages/EditMedia.php:7
* @route '/admin/{tenant}/media/{record}/edit'
*/
EditMedia.url = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions) => {
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

    return EditMedia.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Admin\Resources\Media\Pages\EditMedia::__invoke
* @see app/Admin/Resources/Media/Pages/EditMedia.php:7
* @route '/admin/{tenant}/media/{record}/edit'
*/
EditMedia.get = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditMedia.url(args, options),
    method: 'get',
})

/**
* @see \App\Admin\Resources\Media\Pages\EditMedia::__invoke
* @see app/Admin/Resources/Media/Pages/EditMedia.php:7
* @route '/admin/{tenant}/media/{record}/edit'
*/
EditMedia.head = (args: { tenant: string | number | { uuid: string | number }, record: string | number } | [tenant: string | number | { uuid: string | number }, record: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditMedia.url(args, options),
    method: 'head',
})

export default EditMedia