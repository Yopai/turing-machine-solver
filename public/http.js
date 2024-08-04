export class Http {
    static api(input, init) {
        return new Promise((resolve, reject) => {
            fetch('/api/' + input, init).then((response) => {
                response.json().then(resolve).catch(reject);
            }).catch(reject);
        });
    }
}