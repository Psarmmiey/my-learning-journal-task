const Ziggy = {
    'url': 'http://my-learning-journey-blog.test',
    'port': null,
    'defaults': {},
    'routes': {
        'debugbar.openhandler': {
            'uri': '_debugbar/open',
            'methods': ['GET', 'HEAD'],
        },
        'debugbar.clockwork': {
            'uri': '_debugbar/clockwork/{id}',
            'methods': ['GET', 'HEAD'],
            'parameters': ['id'],
        },
        'debugbar.assets.css': {
            'uri': '_debugbar/assets/stylesheets',
            'methods': ['GET', 'HEAD'],
        },
        'debugbar.assets.js': {
            'uri': '_debugbar/assets/javascript',
            'methods': ['GET', 'HEAD'],
        },
        'debugbar.cache.delete': {
            'uri': '_debugbar/cache/{key}/{tags?}',
            'methods': ['DELETE'],
            'parameters': ['key', 'tags'],
        },
        'sanctum.csrf-cookie': {
            'uri': 'sanctum/csrf-cookie',
            'methods': ['GET', 'HEAD'],
        },
        'profile.edit': { 'uri': 'profile', 'methods': ['GET', 'HEAD'] },
        'profile.update': { 'uri': 'profile', 'methods': ['PATCH'] },
        'profile.destroy': { 'uri': 'profile', 'methods': ['DELETE'] },
        'register': { 'uri': 'register', 'methods': ['GET', 'HEAD'] },
        'login': { 'uri': 'login', 'methods': ['GET', 'HEAD'] },
        'password.request': {
            'uri': 'forgot-password',
            'methods': ['GET', 'HEAD'],
        },
        'password.email': { 'uri': 'forgot-password', 'methods': ['POST'] },
        'password.reset': {
            'uri': 'reset-password/{token}',
            'methods': ['GET', 'HEAD'],
            'parameters': ['token'],
        },
        'password.store': { 'uri': 'reset-password', 'methods': ['POST'] },
        'verification.notice': {
            'uri': 'verify-email',
            'methods': ['GET', 'HEAD'],
        },
        'verification.verify': {
            'uri': 'verify-email/{id}/{hash}',
            'methods': ['GET', 'HEAD'],
            'parameters': ['id', 'hash'],
        },
        'verification.send': {
            'uri': 'email/verification-notification',
            'methods': ['POST'],
        },
        'password.confirm': {
            'uri': 'confirm-password',
            'methods': ['GET', 'HEAD'],
        },
        'password.update': { 'uri': 'password', 'methods': ['PUT'] },
        'logout': { 'uri': 'logout', 'methods': ['POST'] },
        'blog.index': { 'uri': '/', 'methods': ['GET', 'HEAD'] },
        'blog.about': { 'uri': 'blog/about', 'methods': ['GET', 'HEAD'] },
        'blog.my-posts': { 'uri': 'blog/my-posts', 'methods': ['GET', 'HEAD'] },
        'blog.store': { 'uri': 'blog', 'methods': ['POST'] },
        'blog.update': {
            'uri': 'blog/{post}',
            'methods': ['POST'],
            'parameters': ['post'],
            'bindings': { 'post': 'id' },
        },
        'blog.destroy': {
            'uri': 'blog/{post}',
            'methods': ['DELETE'],
            'parameters': ['post'],
            'bindings': { 'post': 'id' },
        },
        'blog.show': {
            'uri': 'blog/{post}',
            'methods': ['GET', 'HEAD'],
            'parameters': ['post'],
            'bindings': { 'post': 'slug' },
        },
    },
};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
