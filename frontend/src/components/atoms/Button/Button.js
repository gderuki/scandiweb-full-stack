import React, { Component } from 'react';

class Button extends Component {
    render() {
        const { label, onClick, icon, style, className, disabled } = this.props;
        return (
            <button onClick={onClick} style={style} className={className} disabled={disabled}>
                {icon && <span>{icon}</span>}   
                {label && <span>{label}</span>}
            </button>
        );
    }
}

export default Button;