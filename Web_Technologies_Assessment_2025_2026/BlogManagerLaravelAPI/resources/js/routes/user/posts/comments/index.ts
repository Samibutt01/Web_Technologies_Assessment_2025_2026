import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:120
 * @route '/user/posts/{id}/comments'
 */
export const store = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/user/posts/{id}/comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:120
 * @route '/user/posts/{id}/comments'
 */
store.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return store.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:120
 * @route '/user/posts/{id}/comments'
 */
store.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:120
 * @route '/user/posts/{id}/comments'
 */
    const storeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::store
 * @see app/Http/Controllers/UserController.php:120
 * @route '/user/posts/{id}/comments'
 */
        storeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:133
 * @route '/user/posts/{postId}/comments/{commentId}'
 */
export const destroy = (args: { postId: string | number, commentId: string | number } | [postId: string | number, commentId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/user/posts/{postId}/comments/{commentId}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:133
 * @route '/user/posts/{postId}/comments/{commentId}'
 */
destroy.url = (args: { postId: string | number, commentId: string | number } | [postId: string | number, commentId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    postId: args[0],
                    commentId: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        postId: args.postId,
                                commentId: args.commentId,
                }

    return destroy.definition.url
            .replace('{postId}', parsedArgs.postId.toString())
            .replace('{commentId}', parsedArgs.commentId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:133
 * @route '/user/posts/{postId}/comments/{commentId}'
 */
destroy.delete = (args: { postId: string | number, commentId: string | number } | [postId: string | number, commentId: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:133
 * @route '/user/posts/{postId}/comments/{commentId}'
 */
    const destroyForm = (args: { postId: string | number, commentId: string | number } | [postId: string | number, commentId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\UserController::destroy
 * @see app/Http/Controllers/UserController.php:133
 * @route '/user/posts/{postId}/comments/{commentId}'
 */
        destroyForm.delete = (args: { postId: string | number, commentId: string | number } | [postId: string | number, commentId: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const comments = {
    store: Object.assign(store, store),
destroy: Object.assign(destroy, destroy),
}

export default comments