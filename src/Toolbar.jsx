import React from 'react';
import './App.css';

class Toolbar extends React.Component {

    constructor(props) {
        super(props);

        this.isAdmin = props.administrator;
        this.auth = props.auth;
    }

    render() {
        return (
            <p>Hello </p>
        );
    }
}

export default Toolbar;