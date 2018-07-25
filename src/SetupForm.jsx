import React from 'react';
import './App.css';

import { Button, Form, FormGroup, Label, Input, Fade } from 'reactstrap';

import Network from './Network';

import * as math from 'mathjs';

class SetupForm extends React.Component {
    /* Instantiate the class with variables needed. */
    constructor(props) {
        super(props);

        this.state = {
            host: "",
            user: "",
            pass: "",
            database: "",
            fadeForm: false
        };

        this.timer = 0;

        // Register methods.
        this.setup = this.setup.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.finishLoading = this.finishLoading.bind(this);
    }


    /* Finish displaying the loading using this method's bind. */
    finishLoading() {
        this.props.loader("ReserveSystem has been setted up.", false);
    }



    /* Set states for the input into application with the value. */
    handleInput(e) {
        this.setState({
            [e.target.name]: e.target.value
        });
    }



    /* Setup button click. */
    async setup() {
        // Fade the form.
        this.setState({
            fadeForm: true
        });

        // Start displaying loadings... to the main app.
        this.props.loader("Setting up the ReserveSystem");


        // Get the parameters out of states.
        let params = [
            this.state.host,
            this.state.user,
            this.state.pass,
            this.state.database
        ];


        // Set a datetime for detecting how long process took, so we can hide the Loading... text.
        let timePassed = Date.now ();
        
        // Call the API, and get the JSON result.
        let json = await Network.call("setup", params);
        
        // In case the setup fails, take back the form visibility.
        if (!json.status) {
            this.setState({
                fadeForm: false
            });
        }


        // After all the API process, let's get time now.
        let timeNow = Date.now ();

        // Difference between times.
        let diff = math.abs(timePassed- timeNow);

        // More than 5 seconds passed, so we no longer need the loading.
        if (diff > 5000) {
            // The second argument is for disabling the display of loading.
            this.props.loader("Reserve system has been setted up.", false);
        }

        // For less than 5 seconds pass, we need to put our difference seconds into a timer.
        // So we can at minimum display 5 seconds of loading, no matter what happens.
        this.timer = setTimeout(this.finishLoading, 5000 - diff);


        // Send a notification to the main app.
        this.props.notify(json.data, json.status);
    }


    /* Render the setup form. */
    render() {
        return (
            <div id="extcontent">
                <Fade in={!this.state.fadeForm}>
                <Form>
                    <FormGroup>
                        <Label for="host">Database Host</Label>
                        <Input type="text" name="host" id="host" placeholder="Host" onChange={this.handleInput} />
                    </FormGroup>

                    <FormGroup>
                        <Label for="user">Database Username</Label>
                        <Input type="text" name="user" id="user" placeholder="Username" onChange={this.handleInput} />
                    </FormGroup>

                    <FormGroup>
                        <Label for="pass">Database Password</Label>
                        <Input type="password" name="pass" id="pass" placeholder="Password" onChange={this.handleInput} />
                    </FormGroup>

                    <FormGroup>
                        <Label for="pass">Database Name</Label>
                        <Input type="text" name="database" id="database" placeholder="Database" onChange={this.handleInput} />
                    </FormGroup>

                    <Button onClick={this.setup} color="success">Setup</Button>
                </Form>
                </Fade>
            </div>
        );
    }
}


export default SetupForm;