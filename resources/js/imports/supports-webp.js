export default {
    detect: (store) => {
        const webpTestImages = {
            lossy: "UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AAAAAA",
            lossless: "UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==",
            alpha: "UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAABQBAJ0BKgEAAQAAAP4AAA3AAP7mtQAAAA=="
        };

        const results = {
            lossy: null,
            lossless: null,
            alpha: null
        };

        const getResultsValues = () => {
            return Object.keys(results).map((feature) => {
                return results[feature];
            });
        };

        const callback = (feature, result) => {
            results[feature] = result;

            if (getResultsValues().indexOf(null) === -1) {
                store.commit("setSupportsWebP", getResultsValues().indexOf(false) === -1);
                console.log(store.getters.getSupportsWebP);
            }
        };

        const checkFeature = (feature) => {
            const img = new Image();

            img.onload = function() {
                callback(feature, img.width > 0 && img.height > 0);
            };

            img.onerror = function() {
                callback(feature, false);
            };

            img.src = "data:image/webp;base64," + webpTestImages[feature];
        };

        Object.keys(webpTestImages).forEach((feature) => {
            checkFeature(feature);
        });
    }
};
