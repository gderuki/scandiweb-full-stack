import React, { Component } from 'react';

class Button extends Component {
    render() {
        const { label, onClick, icon, style } = this.props;
        return (
            <button onClick={onClick} style={style}>
                {icon && <span>{icon}</span>}   
                {label && <span>{label}</span>}
            </button>
        );
    }
}

export default Button;