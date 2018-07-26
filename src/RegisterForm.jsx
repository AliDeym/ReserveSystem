import React from 'react';
import './App.css';

import { Button, Form, FormGroup, Label, Input, Fade, Col, Container, Row } from 'reactstrap';

import Network from './Network';

import DatePicker from 'react-date-picker';

import * as math from 'mathjs';

class RegisterForm extends React.Component {
    /* Instantiate the class with variables needed. */
    constructor(props) {
        super(props);

        this.state = {
            user: "",
            pass: "",
            firstname: "",
            lastname: "",
            fathername: "",
            phonenumber: "",
            birthdate: new Date(),
            fadeForm: false,
            registerSuccess: false
        };

        this.timer = 0;

        // Register methods.
        this.register = this.register.bind(this);
        this.handleInput = this.handleInput.bind(this);
        this.finishLoading = this.finishLoading.bind(this);
        this.handleDateInput = this.handleDateInput.bind(this);
    }


    /* Finish displaying the loading using this method's bind. */
    finishLoading() {
        this.props.loader("Register successful.", false);
        // Turn off the notification.
        this.props.notify("", true, true);

        /* In case of success, change context. */
        if (this.state.registerSuccess) {
            this.props.context("LoginForm");
        }
    }



    /* Set states for the input into application with the value. */
    handleInput(e) {
        // Replace non numbers in phone number and user code.
        if ((e.target.name === "phonenumber" || e.target.name === "code") && e.target.value.match(/[^$,.\d]/)) {
            e.target.value = e.target.value.replace(/[^$,.\d]/, "");
        }

        this.setState({
            [e.target.name]: e.target.value
        });
    }


    /* Handles date input and puts it into states. */
    handleDateInput(dateVal) {
        this.setState({
            birthdate: dateVal
        });
    }



    /* Setup button click. */
    async register() {
        // Fade the form.
        this.setState({
            fadeForm: true
        });

        // Start displaying loadings... to the main app.
        this.props.loader("Registering");

        // Get the parameters out of states.
        let params = [
            this.state.code,
            this.state.pass,
            this.state.firstname,
            this.state.lastname,
            this.state.fathername,
            this.state.phonenumber,
            this.state.birthdate.getTime() / 1000
        ];


        // Set a datetime for detecting how long process took, so we can hide the Loading... text.
        let timePassed = Date.now();

        // Call the API, and get the JSON result.
        let json = await Network.call("register", params);

        this.setState({
            registerSuccess: json.status
        });

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
            this.finishLoading();
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
                            <Label for="confirmpass">Confirm Password</Label>
                            <Input type="password" name="confirmpass" id="confirmpass" placeholder="Confirm Password" onChange={this.handleInput} />
                        </FormGroup>




                        <FormGroup>
                            <Label for="firstname">First Name</Label>
                            <Input type="text" name="firstname" id="firstname" placeholder="First Name" onChange={this.handleInput} />
                        </FormGroup>

                        <FormGroup>
                            <Label for="lastname">Last Name</Label>
                            <Input type="text" name="lastname" id="lastname" placeholder="Last Name" onChange={this.handleInput} />
                        </FormGroup>

                        <FormGroup>
                            <Label for="fathername">Father Name</Label>
                            <Input type="text" name="fathername" id="fathername" placeholder="Father Name" onChange={this.handleInput} />
                        </FormGroup>

                        


                        <FormGroup>
                            <Label for="phonenumber">Phone Number</Label>
                            <Input type="text" name="phonenumber" id="phonenumber" placeholder="Phone Number" onChange={this.handleInput} />
                        </FormGroup>


                        <FormGroup>
                            <Label for="date">Birth Date</Label>
                            <Row>
                                <Col xs="12">
                                    <DatePicker
                                        name="date"
                                        id="date"
                                        onChange={this.handleDateInput}
                                        value={this.state.birthdate}
                                    />
                                </Col>
                            </Row>
                        </FormGroup>

                        <FormGroup>
                            <Container>
                                <Row>
                                    <Col xs="12">
                                        <Button onClick={this.register} color="info">Register</Button>
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


export default RegisterForm;