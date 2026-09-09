import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CmsPreviewController::__invoke
* @see app/Http/Controllers/CmsPreviewController.php:10
* @route '/cms/preview/{content}'
*/
const CmsPreviewController = (args: { content: number | { id: number } } | [content: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CmsPreviewController.url(args, options),
    method: 'get',
})

CmsPreviewController.definition = {
    methods: ["get","head"],
    url: '/cms/preview/{content}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CmsPreviewController::__invoke
* @see app/Http/Controllers/CmsPreviewController.php:10
* @route '/cms/preview/{content}'
*/
CmsPreviewController.url = (args: { content: number | { id: number } } | [content: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { content: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { content: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            content: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        content: typeof args.content === 'object'
        ? args.content.id
        : args.content,
    }

    return CmsPreviewController.definition.url
            .replace('{content}', parsedArgs.content.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CmsPreviewController::__invoke
* @see app/Http/Controllers/CmsPreviewController.php:10
* @route '/cms/preview/{content}'
*/
CmsPreviewController.get = (args: { content: number | { id: number } } | [content: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CmsPreviewController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CmsPreviewController::__invoke
* @see app/Http/Controllers/CmsPreviewController.php:10
* @route '/cms/preview/{content}'
*/
CmsPreviewController.head = (args: { content: number | { id: number } } | [content: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CmsPreviewController.url(args, options),
    method: 'head',
})

export default CmsPreviewController