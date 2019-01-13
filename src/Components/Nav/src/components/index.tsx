import React, { Component, Fragment } from 'react'
import Portal from '~components/Helper/src/components/portal'
import './style'

class Nav extends Component {
  public container = document.querySelector('.inn-nav') as HTMLElement
  public getMods() {
    const mods = document.querySelectorAll('.inn-mod') as NodeListOf<
      HTMLElement
    >

    return Array.from(mods).map((item: any) => {
      return {
        id: item.id,
        full: item.querySelector('.inn-mod__title__text.is-full').textContent,
        tiny: item.querySelector('.inn-mod__title__text.is-tiny').textContent,
      }
    })
  }

  public render() {
    if (!this.container) {
      return null
    }

    return (
      <Portal target={this.container}>
        <>
          {this.getMods().map(({ id, full, tiny }) => {
            return (
              <Fragment key={id}>
                <a href={`#${id}`} className="inn-nav__link is-full">
                  {full}
                </a>
                <a href={`#${id}`} className="inn-nav__link is-tiny">
                  {tiny}
                </a>
              </Fragment>
            )
          })}
        </>
      </Portal>
    )
  }
}

export default Nav
