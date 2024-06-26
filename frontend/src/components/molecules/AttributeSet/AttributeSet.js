import React, { Component } from 'react';
import './AttributeSet.css';

class AttributeSet extends Component {
  renderGroup(groupName, items) {
    switch (groupName) {
      case 'colors':
        return (
          <div className="item-container">
            {items.map((item, index) => (
              <div key={index} className={`item-color ${index === 2 ? 'active' : ''}`} style={{ backgroundColor: item.hex }}></div>
            ))}
          </div>
        );
      case 'sizes':
        return (
          <div className="item-container">
            {items.map((item, index) => (
              <div key={index} className={`item-text ${index === 1 ? 'active' : ''}`}>{item}</div>
            ))}
          </div>
        );
      case 'availability':
        return (
          <div className="item-container">
            <label className="active" style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
              <input type="checkbox" checked={items[0]} readOnly />
              Available
            </label>
            <label style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
              <input type="checkbox" checked={!items[0]} readOnly />
              Not Available
            </label>
          </div>
        );
      default:
        return null;
    }
  }

  render() {
    const { attributes } = this.props;

    return (
      <div>
        {Object.keys(attributes).map((groupName) => (
          <div key={groupName}>
            <h2 className='attribute-heading'>{groupName.charAt(0).toUpperCase() + groupName.slice(1)}:</h2>
            {this.renderGroup(groupName, attributes[groupName])}
          </div>
        ))}
      </div>
    );
  }
}

export default AttributeSet;