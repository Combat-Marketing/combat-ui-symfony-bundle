import Encore from '@symfony/webpack-encore';

// Manually configure the runtime environment if not already configured yet by
// the "encore" command. It's useful when you use tools that rely on the
// webpack.config.js file, like "webpack" or "webpack-dev-server".
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // The directory where compiled assets get stored.
    .setOutputPath('public/build/')

    // The public path used by the web server to access the output path. This must
    // match how OpenDXP/Symfony serves this bundle's assets:
    //   CombatUICoreBundle -> /bundles/combatuicore
    .setPublicPath('/bundles/combatuicore/build')
    .setManifestKeyPrefix('build/')

    // Entry point: registers every Combat UI custom element and ships the global
    // stylesheet (see @combat-ui/core/auto).
    .addEntry('combat-ui', './public/js/index.js')

    // Blocks/token manifest consumed by CMS wrapper bundles (the OpenDXP theme
    // editor reads token groups and default values from it). Copied unversioned
    // so wrappers can resolve it at a stable path.
    .copyFiles({
        from: './node_modules/@combat-ui/core/dist',
        pattern: /^blocks\.json$/,
        to: '[name].[ext]',
    })

    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction());

export default Encore.getWebpackConfig();