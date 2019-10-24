{{- define "imagePullSecret" }}
{{- printf "{\"auths\":{\"%s\":{\"Username\":\"%s\",\"Password\":\"%s\",\"Email\":\"%s\"}}}" .Values.imageCredentials.registry .Values.imageCredentials.username .Values.imageCredentials.password .Values.imageCredentials.email | b64enc }}
{{- end }}

{{- define "mediaproxy.fullname" -}}
    {{- printf "%s-%s" .Release.Name "mediaproxy" | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{- define "mediaproxy.backend_uri" -}}
    {{- printf "http://%s" (.Release.Name) -}}
{{- end -}}

{{- define "elasticsearch.fullname" -}}
    {{- printf "%s-%s" .Release.Name "elasticsearch" | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{- define "jackrabbit.fullname" -}}
    {{- printf "%s-%s" .Release.Name "jackrabbit" | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{- define "cronjob.fullname" -}}
    {{- printf "%s-%s" .Release.Name "cronjob" | trunc 63 | trimSuffix "-" -}}
{{- end -}}
