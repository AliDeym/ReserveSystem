import React from 'react';

class LoadingText extends React.Component {
    /* Constructor for loading text... */
    constructor (props) {
        super (props);

        this.state = {
            loadingText: "."
        }

        this.timer = 0;

        this.loadingTimer = this.loadingTimer.bind(this);
    }

    /* Garbage collection handling for loading text timer. */
    componentWillUnmount() {
        clearInterval(this.timer);
    }

    /* Start the timer right after component is inserted. */
    componentDidMount() {
        this.timer = setInterval(this.loadingTimer, 500);
    }


    /* Timer function to run. */
    loadingTimer () {
        var text = this.state.loadingText + ".";

        if (this.state.loadingText.length > 3) {
            text = ".";
        }

        this.setState({
            loadingText: text
        });
    }



    render () {
        return (
            <p>{this.props.text + this.state.loadingText}</p>
        );
    }
}

/* Default text properties. */
LoadingText.defaultProps = {
    text: "Loading"
}

export default LoadingText;