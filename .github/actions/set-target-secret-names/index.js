const core = require('@actions/core')

const targetMapping = JSON.parse(core.getInput('target-mapping'))
const branch = core.getInput('ref').replace(RegExp('^refs/(heads|tags)/'), '')
const target = targetMapping[branch]
const secretNames = core.getInput('secrets')

if (target) {
  secretNames.split("\n").forEach(secretName => {
    core.exportVariable(secretName.trim(), target + '_' + secretName.trim())
  })
}
