import React, { Component } from 'react'

import { observer } from 'mobx-react'
import Portal from '~components/Helper/src/components/portal'
import store from '../stores'
import fetchServer from '~components/Helper/src/components/fetch-server'

import './style/index.scss'

@observer
class ServerBenchmark extends Component {
  public container = document.getElementById(
    'inn-benchmark__container'
  ) as HTMLElement

  public store = store

  public onClick = async () => {
    const { conf, isLoading, totalMarks } = this.store

    if (isLoading) {
      return false
    }

    const { loading, retry } = conf.lang

    this.store.setLinkText(loading)
    this.store.setIsLoading(true)

    const res = await fetchServer({
      action: 'benchmark',
    })

    if (res && res.code === 0) {
      this.store.setMarks(res.data.points)
      this.store.setLinkText(`✓ ${res.data.totalHuman}`)
    } else if (res && res.code) {
      const msg = totalMarks
        ? String(res.msg) + ` (${totalMarks})`
        : String(res.msg)
      this.store.setLinkText(msg)
    } else {
      this.store.setLinkText(retry)
    }

    this.store.setIsLoading(false)
  }

  public render() {
    const { linkText } = this.store

    return (
      <Portal target={this.container}>
        <a
          onClick={this.onClick}
          className="inn-benchmark__link inn-tooltip is-top"
          title={this.store.linkTitle}
        >
          {linkText}
        </a>
      </Portal>
    )
  }
}

export default ServerBenchmark
