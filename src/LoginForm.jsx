import React from 'react';
import './App.css';

import { Button, Form, FormGroup, Label, Input, Fade, Col, Container, Row } from 'reactstrap';

import Network from './Network';

import * as math from 'mathjs';

class LoginForm extends React.Component {
    /* Instantiate the class with variables needed. */
    constructor(props) {
        super(props);

        this.state = {
            user: "",
            pass: "",
            fadeForm: false
        };

        this.timer = 0;

        // Register methods.
        this.login = this.login.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.finishLoading = this.finishLoading.bind(this);
    }


    /* Finish displaying the loading using this method's bind. */
    finishLoading() {
        this.props.loader("Log in successful.", false);
    }



    /* Set states for the input into application with the value. */
    handleInput(e) {
        this.setState({
            [e.target.name]: e.target.value
        });
    }



    /* Setup button click. */
    async login() {
        // Fade the form.
        this.setState({
            fadeForm: true
        });

        // Start displaying loadings... to the main app.
        this.props.loader("Logging in");


        // Get the parameters out of states.
        let params = [
            this.state.code,
            this.state.pass
        ];


        // Set a datetime for detecting how long process took, so we can hide the Loading... text.
        let timePassed = Date.now();

        // Call the API, and get the JSON result.
        let json = await Network.call("login", params);

        // In case the setup fails, take back the form visibility.
        if (!json.status) {
            this.setState({
                fadeForm: false
            });
        }


        // After all the API process, let's get time now.
        let timeNow = Date.now();

        // Difference between times.
        let diff = math.abs(timePassed - timeNow);

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
                            <Label for="code">Usercode</Label>
                            <Input type="text" name="code" id="code" placeholder="Usercode" onChange={this.handleInput} />
                        </FormGroup>

                        <FormGroup>
                            <Label for="pass">Password</Label>
                            <Input type="password" name="pass" id="pass" placeholder="Password" onChange={this.handleInput} />
                        </FormGroup>

                        <FormGroup>
                            <Container>
                                <Row>
                                    <Col xs="6">
                                        <Button onClick={this.login} color="success">Login</Button>
                                    </Col>
                                    <Col xs="6">
                                        <Button onClick={(e) => this.props.context("SetupForm") } color="info">Register</Button>
                                    </Col>
                                </Row>
                            </Container>
                        </FormGroup>
                    </Form>
                </Fade>
            </div>
        );
    }
}


export default LoginForm;