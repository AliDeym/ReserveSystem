import React, { Component } from 'react';
import './App.css';
import logo from './logo.svg';

import SetupForm from './SetupForm.jsx';
import LoginForm from './LoginForm.jsx';

import Notifications from './Notifications';
import LoadingText from './LoadingText';

import { Collapse } from 'reactstrap';

import Network from './Network';



class App extends Component {
  /* Constructor for app, set the default variables. */
  constructor(props) {
    super(props);

    this.state = {
      alertComponent: "",
      mainContent: "",
      loadingText: "Loading",
      fadeForm: true,
      loadingFaded: true
    }

    this.notifyHandler = this.notifyHandler.bind(this);
    this.loadingHandler = this.loadingHandler.bind(this);
    this.contentChangeHandler = this.contentChangeHandler.bind(this);
  }

  /* Handle notifications insertion. */
  notifyHandler(message = "Error", success = false) {
    this.setState({
      alertComponent: <Notifications color={success ? "success" : "danger"} message={message} />
    });
  }

  /* Handles showing loadings. */
  loadingHandler(message = "Loading", show = true) {
    this.setState({
      loadingFaded: !show,
      loadingText: message
    });
  }

  contentChangeHandler(componentName = "LoginForm") {
    // Create a list of components that we want to use.
    let components = {
      SetupForm: SetupForm,
      LoginForm: LoginForm
    }

    // Get the component dynamically with the string variable from the components list.
    const DynamicComponent = components[componentName];

    // Draw the requested component.
    this.setState({
      mainContent: <DynamicComponent notify={this.notifyHandler} loader={this.loadingHandler} context={this.contentChangeHandler} />
    });
  }

  /* Detect if app is loaded or not, then display setup or the login form. */
  async componentDidMount() {
    // Call the web server.
    let json = await Network.call("app");


    // Get the requested form type with the JSON's status variable. 
    let mod = json.status ? "LoginForm" : "SetupForm";

    // Change our content.
    this.contentChangeHandler(mod);
  }

  render() {
    return (
      <div className="App">
        <header className="App-header">
          <div>
            <h1 className="App-title">ReserveSystem - Made with React</h1>
            <p className="App-copyright">Copyright © 2018 - Ali Deym (self@alideym.net)</p>
          </div>
        </header>


        <div className="App-Content">
          <div className="mcontents">
            <div>
              {this.state.alertComponent}
            </div>

            <Collapse isOpen={!this.state.loadingFaded}>
              <img src={logo} className="App-logo" alt="logo" />
              <LoadingText text={this.state.loadingText} />
            </Collapse>

            {this.state.mainContent}
          </div>
        </div>
      </div>
    );
  }
}

export default App;