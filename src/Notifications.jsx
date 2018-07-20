import React from 'react';

import { Alert } from 'reactstrap';

/* Notifications alert class */
class Notifications extends React.Component {
    render() {
        return (
            <Alert color={this.props.color}>
                {this.props.message}
            </Alert>
        );
    }
}

/* Set the default properties in case of no properties enter. */
Notifications.defaultProps = {
    error: "success",
    message: "Setup finished!"
}

export default Notifications;