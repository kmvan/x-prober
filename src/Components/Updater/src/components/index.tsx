import React, { Component } from 'react'

import { observer } from 'mobx-react'
import Portal from '~components/Helper/src/components/portal'
import store from '../stores'
import fetchServer from '~components/Helper/src/components/fetch-server'

@observer
class Updater extends Component {
  public container = document.getElementById('inn-title') as HTMLElement
  public store = store

  public componentDidMount() {
    this.checkUpdate()
  }

  private versionCompare(left, right) {
    if (typeof left + typeof right !== 'stringstring') {
      return false
    }

    const a = left.split('.')
    const b = right.split('.')
    const len = Math.max(a.length, b.length)

    for (let i = 0; i < len; i++) {
      if ((a[i] && !b[i] && ~~a[i] > 0) || ~~a[i] > ~~b[i]) {
        return 1
      } else if ((b[i] && !a[i] && ~~b[i] > 0) || ~~a[i] < ~~b[i]) {
        return -1
      }
    }

    return 0
  }

  public getVersionInfo(data) {
    const reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/gm
    return reg.test(data) ? [RegExp.$1, RegExp.$2] : []
  }

  private getChangelogInfo() {
    const { conf } = this.store

    return new Promise(resolve => {
      const xhr = new XMLHttpRequest()
      xhr.open('get', conf.changelogUrl)
      xhr.send()
      xhr.addEventListener('load', () => {
        resolve(xhr.responseText)
      })
      xhr.addEventListener('error', () => {
        resolve('')
      })
    })
  }

  public checkUpdate = async () => {
    const { conf, setNewVersion, setTitle } = this.store
    const changelog = await this.getChangelogInfo()

    if (!changelog) {
      return
    }

    const versionInfo = this.getVersionInfo(changelog)

    if (!versionInfo.length) {
      return
    }

    const newVersion = versionInfo[0]

    // no update
    if (this.versionCompare(conf.version, newVersion) !== -1) {
      return
    }

    setNewVersion(newVersion)

    setTitle(
      this.getLink(
        this.goUpdate,
        conf.lang.foundNewVersion.replace('{APP_NEW_VERSION}', newVersion)
      )
    )
  }

  private getLink(onClick, msg) {
    return (
      <a onClick={onClick} className="inn-title__link">
        {msg}
      </a>
    )
  }

  public goUpdate = async () => {
    const { setTitle, isLoading, setIsLoading, conf } = this.store

    if (isLoading) {
      return false
    }

    setIsLoading(true)
    setTitle(this.getLink(() => {}, conf.lang.loading))

    const res = await fetchServer({
      action: 'update',
    })

    if (res && res.code === 0) {
      setTitle(this.getLink(() => location.reload(true), res.msg))
      location.reload(true)
    } else if (res && res.code) {
      setTitle(this.getLink(this.checkUpdate, res.msg))
    } else {
      setTitle(this.getLink(this.checkUpdate, conf.lang.error))
    }

    setIsLoading(false)

    this.store.setIsLoading(false)
  }

  public render() {
    const { newVersion, conf, title } = this.store

    if (newVersion === conf.version) {
      return null
    }

    return <Portal target={this.container}>{title}</Portal>
  }
}

export default Updater
