import React from 'react'
import classNames from 'classnames'

const Alert = (type: string, msg) => {
  type = type === 'success' ? 'ok' : type

  const className = classNames({
    'inn-ini': true,
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
      <span className={className} dangerouslySetInnerHTML={{ __html: icon }} />
      <span className="inn-ini__text">{msg}</span>
    </>
  )
}

export default Alert
