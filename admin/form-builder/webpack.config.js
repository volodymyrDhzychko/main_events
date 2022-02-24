module.exports  = (env, argv) => {
    let production = argv.mode === 'production'
    return {
        entry: "./container/FormBuilder.jsx",
        output: {
            path: __dirname,
            filename: "./build/bundle.js"
        },
        devtool: production ? '' : 'source-map',
        module: {
            rules: [
                {
                    test: /.jsx$/,
                    loader: "babel-loader",
                    exclude: /node_modules/,
                    options: {
                        presets: [["env", "react"]],
                        plugins: ["transform-class-properties"]
                    }
                }
            ]
        }
    }
};