import React from 'react';
import ReactDOM from 'react-dom';

function Greet() {
    return (
        <div>
            Hello world!
        </div>
    );
}

export default Greet;

if (document.getElementById('app')) {
    ReactDOM.render(<Greet />, document.getElementById('app'));
}
