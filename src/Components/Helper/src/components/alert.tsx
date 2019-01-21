import React from 'react'
import classNames from 'classnames'

const Alert = (type: string, msg = '') => {
  type = type === 'success' ? 'ok' : type

  const className = classNames({
    'inn-alert': true,
    [`is-${type}`]: true,
  })

  let icon = '&times;'

  switch (type) {
    case 'ok':
      icon = '&check;'
    case 'loading':
      icon = '⏳'
  }

  return (
    <>
      <div className={className}>
        <div className="inn-alert__icon">{icon}</div>
        {msg && (
          <span
            className="inn-alert__text"
            dangerouslySetInnerHTML={{ __html: msg }}
          />
        )}
      </div>
    </>
  )
}

export default Alert
