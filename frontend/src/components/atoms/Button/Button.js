import React, { Component } from 'react';

class Button extends Component {
    render() {
        const { label, onClick, icon, style, className } = this.props;
        return (
            <button className={className} onClick={onClick} style={style}>
                {icon && <span>{icon}</span>}   
                {label && <span>{label}</span>}
            </button>
        );
    }
}

export default Button;