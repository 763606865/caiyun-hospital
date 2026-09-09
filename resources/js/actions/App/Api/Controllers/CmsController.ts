import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Api\Controllers\CmsController::categories
* @see app/Api/Controllers/CmsController.php:13
* @route '/api/cms/categories'
*/
export const categories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

categories.definition = {
    methods: ["get","head"],
    url: '/api/cms/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Api\Controllers\CmsController::categories
* @see app/Api/Controllers/CmsController.php:13
* @route '/api/cms/categories'
*/
categories.url = (options?: RouteQueryOptions) => {
    return categories.definition.url + queryParams(options)
}

/**
* @see \App\Api\Controllers\CmsController::categories
* @see app/Api/Controllers/CmsController.php:13
* @route '/api/cms/categories'
*/
categories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

/**
* @see \App\Api\Controllers\CmsController::categories
* @see app/Api/Controllers/CmsController.php:13
* @route '/api/cms/categories'
*/
categories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: categories.url(options),
    method: 'head',
})

/**
* @see \App\Api\Controllers\CmsController::index
* @see app/Api/Controllers/CmsController.php:22
* @route '/api/cms/contents'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/cms/contents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Api\Controllers\CmsController::index
* @see app/Api/Controllers/CmsController.php:22
* @route '/api/cms/contents'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Api\Controllers\CmsController::index
* @see app/Api/Controllers/CmsController.php:22
* @route '/api/cms/contents'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Api\Controllers\CmsController::index
* @see app/Api/Controllers/CmsController.php:22
* @route '/api/cms/contents'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Api\Controllers\CmsController::show
* @see app/Api/Controllers/CmsController.php:43
* @route '/api/cms/contents/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/cms/contents/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Api\Controllers\CmsController::show
* @see app/Api/Controllers/CmsController.php:43
* @route '/api/cms/contents/{slug}'
*/
show.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return show.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Api\Controllers\CmsController::show
* @see app/Api/Controllers/CmsController.php:43
* @route '/api/cms/contents/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Api\Controllers\CmsController::show
* @see app/Api/Controllers/CmsController.php:43
* @route '/api/cms/contents/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const CmsController = { categories, index, show }

export default CmsController