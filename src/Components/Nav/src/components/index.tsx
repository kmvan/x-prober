import React, { Component, Fragment } from 'react'
import Portal from '~components/Helper/src/components/portal'
import './style'

class Nav extends Component {
  public appContainer = document.querySelector('.inn-app')
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

  public scrollTop = (eleId: string) => {
    const ele = document.querySelector(eleId) as HTMLElement
    if (!ele) {
      return
    }

    if (!this.appContainer) {
      return
    }

    // console.log(ele.offsetTop)

    this.appContainer.scrollTo(0, Number(ele.offsetTop) - 40)
  }

  public render() {
    return (
      <Portal>
        <div className="inn-nav">
          {this.getMods().map(({ id, full, tiny }) => {
            return (
              <Fragment key={id}>
                <a
                  onClick={() => this.scrollTop(`#${id}`)}
                  className="inn-nav__link is-full"
                >
                  {full}
                </a>
                <a
                  onClick={() => this.scrollTop(`#${id}`)}
                  className="inn-nav__link is-tiny"
                >
                  {tiny}
                </a>
              </Fragment>
            )
          })}
        </div>
      </Portal>
    )
  }
}

export default Nav
