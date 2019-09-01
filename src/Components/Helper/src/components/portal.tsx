import { Component } from 'react'
import { createPortal } from 'react-dom'

interface IPortal {
  target?: HTMLElement
}

class Portal extends Component<IPortal, {}> {
  private target: HTMLElement

  constructor(props) {
    super(props)

    const { target: propTarget } = this.props

    if (propTarget) {
      propTarget.innerHTML = ''
      this.target = propTarget
    } else {
      this.target = document.createElement('div')
    }
  }

  public componentDidMount() {
    this.props.target || document.body.appendChild(this.target)
  }

  public componentWillUnmount() {
    const target = this.target

    if (target) {
      const { parentNode } = target

      parentNode && parentNode.removeChild(target)
    }
  }

  public render() {
    return createPortal(this.props.children, this.target)
  }
}

export default Portal
