import axios from 'axios';

class Network {
    // Base URL of API.
    static apiURL = "http://192.168.1.2/API/";

    // Call the network library and return it's value asynchronous.
    static async call(method, params) {
        let json;

        let rawParams = "";

        // Concat every parameter in array to one single string.
        if (params != null) {
            await params.map(arg =>
                rawParams = rawParams + arg + "/"
            );
        }

        await axios.get(this.apiURL + method + ".php/" + rawParams).then(res => {
            json = res.data;
        }).catch(err => {
            alert(err);
        });

        return json;
    }
}

export default Network;